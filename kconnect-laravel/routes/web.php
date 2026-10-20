<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| K-Connect routes
|--------------------------------------------------------------------------
| Every page keeps its old address (login.php, admin/dashboard.php, ...), so bookmarks,
| links stored in navigation_links and emails that were already sent keep working.
| Each area has its own file in routes/kconnect/.
*/

foreach (glob(__DIR__.'/kconnect/*.php') as $file) {
    require $file;
}

// Test helper used by the old-vs-new comparison script; only when KC_TEST_ROUTES=true.
if (env('KC_TEST_ROUTES')) {
    Route::get('/__csrf', fn () => csrf_token());
}

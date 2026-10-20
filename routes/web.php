<?php

use App\Http\Middleware\RedirectLegacyPhpUrl;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| K-Connect routes
|--------------------------------------------------------------------------
| Each area has its own file in routes/kconnect/. Pages use clean addresses
| (/employee-directory, /admin/dashboard). Every route gets a name made from its
| address (employee-directory, admin.dashboard) for use with route().
|
| The old .php addresses still work: visiting one redirects to the clean address,
| and forms or AJAX calls posting to one are still answered.
*/

foreach (glob(__DIR__.'/kconnect/*.php') as $file) {
    require $file;
}

Route::redirect('/admin', '/admin/dashboard');

foreach (Route::getRoutes()->getRoutes() as $route) {
    $uri = $route->uri();
    if ($uri === '/' || str_ends_with($uri, '.php') || $route->getName() !== null) {
        continue;
    }
    $route->name(str_replace('/', '.', $uri));
    Route::match($route->methods(), '/'.$uri.'.php', $route->getAction('uses'))
        ->middleware(RedirectLegacyPhpUrl::class);
}
Route::get('/index.php', fn () => redirect('/', 301));
Route::any('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Test helper used by the old-vs-new comparison script; only when KC_TEST_ROUTES=true.
if (env('KC_TEST_ROUTES')) {
    Route::get('/__csrf', fn () => csrf_token());
}

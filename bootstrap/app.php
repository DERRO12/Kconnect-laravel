<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        // Anonymous survey API and form: no session, cookies or CSRF (routes/public.php)
        then: fn () => \Illuminate\Support\Facades\Route::group([], base_path('routes/public.php')),
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // The old pages read $_POST values untouched: keep spaces and empty strings as sent.
        $middleware->remove([
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\InjectCsrfToken::class,
            \App\Http\Middleware\ClearContentCache::class,
            \App\Http\Middleware\SpeedUpPages::class,
        ]);
        // The hosting proxy (cPanel / Cloudflare) passes the visitor's IP like before
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

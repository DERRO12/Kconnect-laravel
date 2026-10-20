<?php

namespace App\Providers;

use Illuminate\Database\Events\StatementPrepared;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use PDO;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Rows come back as associative arrays, exactly like mysqli_fetch_assoc(),
        // so pages keep using $row['column'] (Eloquent models also allow $model['column']).
        Event::listen(StatementPrepared::class, function (StatementPrepared $event) {
            $event->statement->setFetchMode(PDO::FETCH_ASSOC);
        });

        // The old site printed PHP warnings/notices and carried on. Laravel would stop with an
        // error page. Unless KC_STRICT_ERRORS=true, warnings are written to the log and the page
        // carries on, as before.
        if (! filter_var(env('KC_STRICT_ERRORS', false), FILTER_VALIDATE_BOOL)) {
            $previous = set_error_handler(null);
            set_error_handler(function ($level, $message, $file = '', $line = 0) use (&$previous) {
                if (in_array($level, [E_WARNING, E_NOTICE, E_USER_WARNING, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED], true)) {
                    Log::warning("PHP warning: $message in $file:$line");

                    return true;
                }

                return $previous ? $previous($level, $message, $file, $line) : false;
            });
        }
    }
}

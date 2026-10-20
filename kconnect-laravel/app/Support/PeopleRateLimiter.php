<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Rate limiter for password reset (was includes/rate-limiter.php).
 * Prevents abuse of the password reset functionality.
 * Note: the old forgot-password.php never called it; it is kept for completeness.
 */
class PeopleRateLimiter
{
    public static function checkPasswordResetLimit($employee_code)
    {
        $ip = request()->ip() ?? 'unknown';
        $time_limit = 3600; // 1 hour in seconds
        $max_attempts = 3;   // Max 3 attempts per hour

        // Clean up old attempts
        DB::delete('DELETE FROM password_reset_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 1 HOUR)');

        // Check recent attempts
        $row = DB::selectOne('SELECT COUNT(*) as attempts FROM password_reset_attempts 
              WHERE (employee_code = ? OR ip_address = ?) 
              AND attempt_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)', [$employee_code, $ip]);

        if ($row['attempts'] >= $max_attempts) {
            return false;
        }

        // Log the attempt
        $user_agent = request()->userAgent() ?? '';
        DB::insert('INSERT INTO password_reset_attempts (employee_code, ip_address, user_agent, attempt_time) 
               VALUES (?, ?, ?, NOW())', [$employee_code, $ip, $user_agent]);

        return true;
    }
}

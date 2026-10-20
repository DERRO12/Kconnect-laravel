<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `password_reset_attempts` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string|null $employee_code  varchar(50)
 * @property string $ip_address  varchar(45)
 * @property string|null $user_agent  text
 * @property string $attempt_time  datetime
 */
class PasswordResetAttempt extends Model
{
    protected $table = 'password_reset_attempts';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

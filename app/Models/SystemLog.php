<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `system_logs` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $user_type  enum('admin','employee')
 * @property string $user_id  int(11)
 * @property string|null $username  varchar(100)
 * @property string|null $full_name  varchar(255)
 * @property string $action  varchar(100)
 * @property string|null $page_accessed  varchar(255)
 * @property string|null $ip_address  varchar(45)
 * @property string|null $user_agent  text
 * @property string|null $details  text
 * @property string $created_at  timestamp
 */
class SystemLog extends Model
{
    protected $table = 'system_logs';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

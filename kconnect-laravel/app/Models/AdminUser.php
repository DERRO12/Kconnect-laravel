<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `admin_users` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $username  varchar(50)
 * @property string $password  varchar(255)
 * @property string|null $email  varchar(100)
 * @property string|null $role  enum('admin','editor')
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class AdminUser extends Model
{
    protected $table = 'admin_users';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    protected $hidden = ['password'];
}

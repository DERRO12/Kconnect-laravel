<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `birthdays` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $full_name  varchar(255)
 * @property string|null $photo  varchar(255)
 * @property string|null $department  varchar(255)
 * @property string $birth_date  date
 * @property string|null $message  text
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class Birthday extends Model
{
    protected $table = 'birthdays';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

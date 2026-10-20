<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `key_contacts` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $name  varchar(255)
 * @property string $position  varchar(255)
 * @property string|null $department  varchar(255)
 * @property string|null $email  varchar(255)
 * @property string|null $phone  varchar(50)
 * @property string|null $extension  varchar(20)
 * @property string|null $mobile  varchar(50)
 * @property string|null $bio  text
 * @property string|null $office_location  varchar(255)
 * @property string|null $office_hours  varchar(255)
 * @property string|null $category  varchar(100)
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class KeyContact extends Model
{
    protected $table = 'key_contacts';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

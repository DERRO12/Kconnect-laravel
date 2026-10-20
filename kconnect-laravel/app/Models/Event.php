<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `events` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $title  varchar(255)
 * @property string|null $description  text
 * @property string $event_date  date
 * @property string|null $event_time  time
 * @property string|null $location  varchar(255)
 * @property string|null $category  varchar(100)
 * @property string|null $image  varchar(255)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class Event extends Model
{
    protected $table = 'events';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

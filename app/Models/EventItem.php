<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `event_items` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $title  varchar(255)
 * @property string $event_date  date
 * @property string|null $event_time  time
 * @property string|null $location  varchar(255)
 * @property string|null $category  enum('Get together','Orientation','Training','Meeting','Social','Other')
 * @property string|null $rsvp_required  tinyint(1)
 * @property string|null $rsvp_url  varchar(500)
 * @property string|null $description  text
 * @property string|null $status  tinyint(1)
 * @property string|null $sort_order  int(11)
 * @property string $created_at  timestamp
 */
class EventItem extends Model
{
    protected $table = 'event_items';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

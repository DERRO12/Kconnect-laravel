<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `announcements` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $title  varchar(255)
 * @property string|null $description  text
 * @property string|null $badge_text  varchar(100)
 * @property string|null $badge_color  varchar(20)
 * @property string|null $action_text  varchar(100)
 * @property string|null $action_url  varchar(255)
 * @property string|null $icon  varchar(50)
 * @property string|null $status  tinyint(1)
 * @property string|null $sort_order  int(11)
 * @property string $created_at  timestamp
 */
class Announcement extends Model
{
    protected $table = 'announcements';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `rewards_items` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $title  varchar(255)
 * @property string|null $description  text
 * @property string|null $video_type  enum('youtube','vimeo','other')
 * @property string $video_url  varchar(500)
 * @property string|null $video_id  varchar(255)
 * @property string|null $status  tinyint(1)
 * @property string|null $sort_order  int(11)
 * @property string|null $views_count  int(11)
 * @property string $created_at  timestamp
 * @property string $updated_at  timestamp
 */
class RewardsItem extends Model
{
    protected $table = 'rewards_items';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

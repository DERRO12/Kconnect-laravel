<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `homepage_carousel` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string|null $title  varchar(255)
 * @property string $media_type  enum('image','youtube','vimeo','social')
 * @property string $media_url  varchar(500)
 * @property string|null $description  text
 * @property string|null $status  tinyint(1)
 * @property string|null $sort_order  int(11)
 * @property string $created_at  timestamp
 */
class HomepageCarousel extends Model
{
    protected $table = 'homepage_carousel';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

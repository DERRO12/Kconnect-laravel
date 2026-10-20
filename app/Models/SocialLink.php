<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `social_links` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $platform  varchar(50)
 * @property string $icon  varchar(100)
 * @property string $url  varchar(500)
 * @property string|null $color  varchar(50)
 * @property string|null $bg_color  varchar(50)
 * @property string|null $username  varchar(255)
 * @property string|null $followers_count  varchar(50)
 * @property string|null $description  text
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class SocialLink extends Model
{
    protected $table = 'social_links';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

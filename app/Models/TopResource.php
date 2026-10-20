<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `top_resources` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $title  varchar(255)
 * @property string $icon  varchar(100)
 * @property string|null $url  varchar(500)
 * @property string|null $color  varchar(50)
 * @property string|null $bg_color  varchar(50)
 * @property string|null $category  varchar(100)
 * @property string|null $description  text
 * @property string|null $target  enum('_self','_blank')
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class TopResource extends Model
{
    protected $table = 'top_resources';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

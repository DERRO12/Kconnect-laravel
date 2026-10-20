<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `gallery_categories` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $name  varchar(100)
 * @property string|null $description  text
 * @property string|null $cover_image  varchar(500)
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class GalleryCategory extends Model
{
    protected $table = 'gallery_categories';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

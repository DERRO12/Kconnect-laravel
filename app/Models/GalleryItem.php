<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `gallery_items` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $title  varchar(255)
 * @property string|null $description  text
 * @property string $thumbnail  varchar(500)
 * @property string $external_url  varchar(500)
 * @property string|null $source_type  enum('onedrive','googledrive','dropbox','youtube','vimeo','other')
 * @property string|null $category  varchar(100)
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 * @property string $updated_at  timestamp
 */
class GalleryItem extends Model
{
    protected $table = 'gallery_items';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

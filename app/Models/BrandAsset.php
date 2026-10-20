<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `brand_assets` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $title  varchar(255)
 * @property string|null $description  text
 * @property string $category  enum('Logos','Documents','Templates','Presentations','Images','Videos','Other')
 * @property string|null $file_type  varchar(50)
 * @property string|null $file_size  int(11)
 * @property string $file_path  varchar(500)
 * @property string|null $thumbnail_path  varchar(500)
 * @property string|null $download_count  int(11)
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 * @property string $updated_at  timestamp
 */
class BrandAsset extends Model
{
    protected $table = 'brand_assets';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

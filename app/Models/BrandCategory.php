<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `brand_categories` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $name  varchar(100)
 * @property string|null $icon  varchar(50)
 * @property string|null $color  varchar(20)
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 */
class BrandCategory extends Model
{
    protected $table = 'brand_categories';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `quick_links` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $title  varchar(100)
 * @property string|null $icon  varchar(50)
 * @property string|null $url  varchar(255)
 * @property string|null $category  varchar(50)
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 */
class QuickLink extends Model
{
    protected $table = 'quick_links';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

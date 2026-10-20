<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Table `navigation_links` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $title  varchar(255)
 * @property string|null $url  varchar(500)
 * @property string|null $parent_id  int(11)
 * @property string|null $is_label  tinyint(1)
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 * @property string|null $target  varchar(10)
 * @property string|null $icon  varchar(50)
 * @property string|null $description  text
 * @property string|null $css_class  varchar(100)
 * @property string|null $role  varchar(50)
 * @property string|null $rel  varchar(100)
 */
class NavigationLink extends Model
{
    protected $table = 'navigation_links';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavigationLink::class, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(NavigationLink::class, 'parent_id', 'id');
    }
}

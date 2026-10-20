<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Table `nomination_categories` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $name  varchar(100)
 * @property string|null $description  text
 * @property string|null $icon  varchar(50)
 * @property string|null $color  varchar(20)
 * @property string|null $is_self_nomination  tinyint(1)
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class NominationCategory extends Model
{
    protected $table = 'nomination_categories';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function nominations(): HasMany
    {
        return $this->hasMany(Nomination::class, 'category_id', 'id');
    }
}

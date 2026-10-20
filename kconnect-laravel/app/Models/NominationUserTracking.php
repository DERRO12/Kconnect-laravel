<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `nomination_user_tracking` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $user_id  int(11)
 * @property string $category_id  int(11)
 * @property string $quarter  tinyint(1)
 * @property string $year  int(11)
 * @property string $nomination_id  int(11)
 * @property string $created_at  timestamp
 */
class NominationUserTracking extends Model
{
    protected $table = 'nomination_user_tracking';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

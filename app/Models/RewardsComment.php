<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `rewards_comments` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $rewards_item_id  int(11)
 * @property string|null $employee_id  int(11)
 * @property string $employee_name  varchar(255)
 * @property string|null $employee_photo  varchar(255)
 * @property string $comment  text
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class RewardsComment extends Model
{
    protected $table = 'rewards_comments';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

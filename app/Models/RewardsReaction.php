<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `rewards_reactions` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $rewards_item_id  int(11)
 * @property string|null $employee_id  int(11)
 * @property string|null $reaction_type  enum('like','celebrate','support','insightful')
 * @property string $created_at  timestamp
 */
class RewardsReaction extends Model
{
    protected $table = 'rewards_reactions';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

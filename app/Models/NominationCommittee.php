<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `nomination_committee` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $employee_id  int(11)
 * @property string $committee_level  tinyint(1)
 * @property string|null $role  varchar(100)
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 * @property string $updated_at  timestamp
 */
class NominationCommittee extends Model
{
    protected $table = 'nomination_committee';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

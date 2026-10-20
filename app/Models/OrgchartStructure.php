<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `orgchart_structure` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $employee_id  int(11)
 * @property string|null $parent_id  int(11)
 * @property string $level  tinyint(2)
 * @property string|null $level_label  varchar(50)
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 * @property string $updated_at  timestamp
 */
class OrgchartStructure extends Model
{
    protected $table = 'orgchart_structure';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

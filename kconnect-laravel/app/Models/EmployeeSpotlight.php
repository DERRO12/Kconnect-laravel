<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `employee_spotlights` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $employee_name  varchar(255)
 * @property string|null $employee_photo  varchar(255)
 * @property string $spotlight_type  enum('employee','team')
 * @property string $achievement_title  varchar(255)
 * @property string|null $achievement_description  text
 * @property string|null $rating  decimal(3,1)
 * @property string|null $department  varchar(100)
 * @property string|null $featured_quote  text
 * @property string|null $badge_text  varchar(100)
 * @property string|null $badge_color  varchar(20)
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class EmployeeSpotlight extends Model
{
    protected $table = 'employee_spotlights';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Table `departments` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $name  varchar(100)
 * @property string|null $code  varchar(20)
 * @property string|null $company_id  int(11)
 * @property string $organization_type  enum('Leadership','Sales','Support')
 * @property string|null $sales_division  enum('P&G','Spark','Star','CD','None')
 * @property string|null $support_department  enum('IT','HR','Internal Audit','Legal','Warehouse','Procurement','Logistics','Administration','Finance','Other')
 * @property string|null $parent_id  int(11)
 * @property string|null $level  int(11)
 * @property string|null $head_employee_id  int(11)
 * @property string|null $description  text
 * @property string|null $icon  varchar(50)
 * @property string|null $color  varchar(20)
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class Department extends Model
{
    protected $table = 'departments';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function headEmployee(): BelongsTo
    {
        return $this->belongsTo(OrganizationChart::class, 'head_employee_id', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_id', 'id');
    }
}

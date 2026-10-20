<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Table `organization_chart` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $employee_name  varchar(255)
 * @property string $position  varchar(255)
 * @property string|null $department  varchar(255)
 * @property string|null $department_id  int(11)
 * @property string|null $sub_department  varchar(255)
 * @property string|null $sub_department_id  int(11)
 * @property string|null $sales_division  enum('P&G','Spark','Star','CD','None')
 * @property string|null $support_department  enum('IT','HR','Internal Audit','Legal','Warehouse','Procurement','Logistics','Administration','Finance','Other')
 * @property string|null $region  varchar(100)
 * @property string|null $office_location  varchar(255)
 * @property string|null $company  enum('KEL Uganda','KEL Tanzania','KFS','Group')
 * @property string|null $company_id  int(11)
 * @property string|null $organization_type  enum('Sales','Support','Leadership')
 * @property string|null $photo  varchar(255)
 * @property string|null $email  varchar(255)
 * @property string|null $phone  varchar(50)
 * @property string|null $parent_id  int(11)
 * @property string|null $level  int(11)
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class OrganizationChart extends Model
{
    protected $table = 'organization_chart';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'head_employee_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `backup_logs` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $backup_date  date
 * @property string $employee_id  int(11)
 * @property string|null $companies  text
 * @property string $status  enum('Completed','Partial','Failed')
 * @property string|null $notes  text
 * @property string|null $screenshot  varchar(255)
 * @property string|null $ticket_id  int(11)
 * @property string $created_at  timestamp
 */
class BackupLog extends Model
{
    protected $table = 'backup_logs';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

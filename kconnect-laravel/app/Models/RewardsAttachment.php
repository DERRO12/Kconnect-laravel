<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `rewards_attachments` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $rewards_item_id  int(11)
 * @property string $file_name  varchar(255)
 * @property string $file_path  varchar(500)
 * @property string|null $file_size  int(11)
 * @property string|null $file_type  varchar(100)
 * @property string|null $download_count  int(11)
 * @property string $created_at  timestamp
 */
class RewardsAttachment extends Model
{
    protected $table = 'rewards_attachments';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

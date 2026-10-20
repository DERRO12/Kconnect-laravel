<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `news_events` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $title  varchar(255)
 * @property string|null $content  text
 * @property string|null $category  enum('Announcements','Events','People','Milestones')
 * @property string|null $author  varchar(100)
 * @property string|null $author_role  varchar(100)
 * @property string|null $image  varchar(255)
 * @property string|null $comments_count  int(11)
 * @property string|null $likes_count  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class NewsEvent extends Model
{
    protected $table = 'news_events';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}

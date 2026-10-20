<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Table `news_posts` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $title  varchar(255)
 * @property string|null $content  text
 * @property string $category  enum('Announcements','Events','People','Milestones')
 * @property string $author_name  varchar(100)
 * @property string|null $author_role  varchar(100)
 * @property string|null $featured_image  varchar(255)
 * @property string|null $comments_count  int(11)
 * @property string|null $likes_count  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 * @property string $updated_at  timestamp
 */
class NewsPost extends Model
{
    protected $table = 'news_posts';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function newsComments(): HasMany
    {
        return $this->hasMany(NewsComment::class, 'news_id', 'id');
    }

    public function newsImages(): HasMany
    {
        return $this->hasMany(NewsImage::class, 'news_id', 'id');
    }

    public function newsLikes(): HasMany
    {
        return $this->hasMany(NewsLike::class, 'news_id', 'id');
    }
}

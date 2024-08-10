<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;

class ArticleTranslation extends Model
{
    use Searchable, LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blog_article_translations';

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'content' => $this->content
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'article_id', 'title', 'slug', 'meta_title',
        'excerpt', 'content', 'meta_description', 'meta_keyword'
    ];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['title', 'excerpt'];

    /**
     * Set timestamps false.
     * @var bool
     */
    public $timestamps = false;
}

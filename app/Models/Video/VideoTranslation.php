<?php

namespace App\Models\Video;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;

class VideoTranslation extends Model
{
    use Searchable, LogsActivity;

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
        'video_id', 'title', 'slug', 'meta_title', 'sub_title',
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

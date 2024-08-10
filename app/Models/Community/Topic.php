<?php

namespace App\Models\Community;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Topic extends Model implements HasMedia
{
    use SoftDeletes, Translatable, NodeTrait, LogsActivity, InteractsWithMedia, Searchable {
        NodeTrait::usesSoftDelete insteadof Searchable;
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'topics';

    /**
     * Set the translated fields.
     *
     * @var array
     */
    public $translatedAttributes = [
        'name', 'slug', 'description'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'image', 'parent_id'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['status'];

    /**
     * A topic has many posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    /**
     * Media collections for this model.
     *
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('topic')
            ->singleFile();
    }
}

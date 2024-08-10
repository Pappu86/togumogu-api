<?php

namespace App\Models\Blog;

use App\Models\Common\Filter;
use App\Models\Common\Tag;
use App\Models\User;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Article extends Model implements HasMedia
{
    use Translatable, SoftDeletes, LogsActivity, InteractsWithMedia;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blog_articles';

    /**
     * Set the translated fields.
     * @var array
     */
    public $translatedAttributes = [
        'title', 'slug', 'meta_title', 'excerpt',
        'content', 'meta_description', 'meta_keyword'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'status', 'image', 'datetime',
        'meta_image', 'view_count', 'is_featured', 'tracker', 'tracker_start_day',
        'tracker_end_day', 'tracker_range', 'video_url', 'audio_url', 'shortLink',
        'previewLink', 'longLink'
    ];

    /**
     * Monitor every fields and add to activity log.
     *
     * @var bool
     */
    protected static $logFillable = true;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'datetime'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * Return published post
     * @param $query
     * @return mixed
     */
    public function scopePublished($query): mixed
    {
        return $query->where('status', '=', 1);
    }

    /**
     * Return published post
     * @param $query
     * @return mixed
     */
    public function scopeOrderByDate($query): mixed
    {
        return $query->orderBy('datetime', 'desc');
    }

    /**
     * Return date passed post
     * @param $query
     * @return mixed
     */
    public function scopeDatePassed($query): mixed
    {
        return $query->where('datetime', '<=', now());
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A product has many categories.
     *
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'blog_article_category_article');
    }

    /**
     * A article has many filters.
     *
     * @return BelongsToMany
     */
    public function filters(): BelongsToMany
    {
        return $this->belongsToMany(Filter::class, 'blog_article_filter')->withPivot('filter_group_id');
    }

    /**
     * Each article has many tags.
     *
     * @return MorphToMany
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Convert image in different sizes.
     *
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('blog_article')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('featured_one')
                    ->width(308)
                    ->height(173)
                    ->sharpen(10);
            });
    }
}

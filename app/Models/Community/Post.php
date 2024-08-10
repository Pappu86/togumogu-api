<?php

namespace App\Models\Community;

use App\Models\Common\AgeGroup;
use App\Models\Common\Hashtag;
use App\Models\User\Customer;
use App\Models\Community\PostImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, Searchable, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'status', 'is_anonymous', 
        'title', 'content', 'slug', 'age_group_id',
        'visible', 'view_count', 'share_count'
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function ageGroup()
    {
        return $this->belongsTo(AgeGroup::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
    }

    public function likes()
    {
        return $this->hasMany(Vote::class)->whereNotNull('like')->where('like', '=', 1);
    }

    public function dislikes()
    {
        return $this->hasMany(Vote::class)->whereNotNull('dislike')->where('dislike', '=', 1);
    }

    // public function loves()
    // {
    //     return $this->hasMany(Vote::class)->whereNotNull('love')->where('love', '=', 1);
    // }

    public function favorites()
    {
        return $this->hasMany(Favourite::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class)->latest();
    }

    /**
     * A post has many topics.
     *
     * @return BelongsToMany
     */
    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, "post_topic_post");
    }

    /**
     * Each video has many tags.
     *
     * @return MorphToMany
     */
    public function hashtags(): MorphToMany
    {
        return $this->morphToMany(Hashtag::class, 'hashtaggable');
    }

     /**
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class);
    }

     /**
     * Convert image in different sizes.
     *
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('post')
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

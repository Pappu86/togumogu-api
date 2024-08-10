<?php

namespace App\Models\Marketing;

use App\Models\Brand\Brand;
use App\Models\Brand\Category;
use App\Models\Common\Tag;
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

class Offer extends Model implements HasMedia
{
    use Translatable, SoftDeletes, LogsActivity, InteractsWithMedia;

    /**
     * Set the translated fields.
     * @var array
     */
    public $translatedAttributes = [
        'title', 'slug', 'short_description', 'long_description'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    // Brand language enum is bn, en
    // Brand type enum is live, recoreded-live, recoreded
    protected $fillable = [
        'status', 'is_togumogu_offer', 'is_free', 'is_featured', 'is_promoted',
        'reward_amount', 'validity_day', 'image', 'card_image', 'brand_id',
        'video_url', 'coupon', 'start_date', 'end_date', 'website_url',
        'website_btn', 'store_location_url', 'store_location_btn'
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
        'status' => 'boolean',
        'is_promoted' => 'boolean',
        'is_featured' => 'boolean',
        'is_free' => 'boolean',
        'is_togumogu_offer' => 'boolean',
    ];

    /**
     * A brand has many categories.
     *
     * @return BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * A brand has many categories.
     *
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'offer_category_offer');
    }

    /**
     * Each brand has many tags.
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
        $this->addMediaCollection('offer')
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

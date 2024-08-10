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

class Service extends Model implements HasMedia
{
    use Translatable, SoftDeletes, LogsActivity, InteractsWithMedia;

    /**
     * Set the translated fields.
     * @var array
     */
    public $translatedAttributes = [
        'title', 'slug', 'short_description', 'long_description', 
        'later_btn_text', 'now_btn_text', 'external_btn_text',
        'reg_btn_text', 'booking_btn_text', 'cta_btn_text',
        'special_price_message', 'external_url_btn_text'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    // Brand language enum is bn, en
    // Brand type enum is live, recoreded-live, recoreded
    protected $fillable = [
        'status', 'view_count', 'is_featured', 'is_promoted', 'registration_count',
        'image', 'brand_id', 'external_url', 'external_payment_url',
        'video_url', 'start_date', 'end_date', 'booking_start_date', 'booking_end_date',
        'price', 'special_price', 'special_price_start_date', 'special_price_end_date',
        'payment_method', 'booking_type', 'is_customer_name', 'is_customer_phone',
        'is_customer_email', 'is_child_name', 'is_child_age', 'is_child_gender',
        'tracker', 'tracker_start_day', 'tracker_end_day', 'tracker_range',
        'is_additional_qus', 'questions', 'is_reg', 'is_booking', 'is_payment',
        'provider_email', 'provider_phone'
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
        'is_reg' => 'boolean',
        'is_customer_name' => 'boolean',
        'is_customer_phone' => 'boolean',
        'is_customer_email' => 'boolean',
        'is_child_name' => 'boolean',
        'is_child_age' => 'boolean',
        'is_child_gender' => 'boolean',
        'is_booking' => 'boolean',
        'is_payment' => 'boolean',
        'is_additional_qus' => 'boolean',
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
        return $this->belongsToMany(Category::class, 'service_category_service');
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
        $this->addMediaCollection('service')
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

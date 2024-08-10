<?php

namespace App\Models\Brand;

use App\Models\Common\Tag;
use App\Models\Corporate\Company;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Brand extends Model implements HasMedia
{
    use Translatable, SoftDeletes, LogsActivity, InteractsWithMedia;

    /**
     * Set the translated fields.
     * @var array
     */
    public $translatedAttributes = [
        'name', 'slug', 'short_description', 'long_description'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    // Brand language enum is bn, en
    // Brand type enum is live, recoreded-live, recoreded
    protected $fillable = [
        'status', 'is_togumogu_partner', 'website_link',
        'longitude', 'logo', 'banner', 'video_url', 'company_id',
        'area_id', 'district_id', 'division_id', 'address_line', 'country',
        'social_links', 'latitude'
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
        'is_togumogu_partner' => 'boolean',
        'social_links' => 'array'
    ];

    /**
     * A brand has many categories.
     *
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'brand_category_brand');
    }

    /**
     * A company has many employee.
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
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
        $this->addMediaCollection('brand')
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

<?php

namespace App\Models\Daycare;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Daycare extends Model
{
    use HasFactory, SoftDeletes, Translatable, Searchable;

    /**
     * Set the translated fields.
     *
     * @var array
     */
    public $translatedAttributes = [
        'name', 'slug', 'description', 'content',
        'meta_title', 'meta_description', 'meta_keyword',
        'hospital_address', 'location', 'division_id', 'district_id', 'area_id',
        'house', 'street', 'zip',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'status', 'code', 'is_featured', 'daycare_category_id',
        'contact', 'latitude', 'longitude', 'social_links', 'year',
        'rooms', 'care_givers', 'capacity', 'booked', 'area',
        'age_range', 'time_range', 'opening_days', 'fees', 'image',
        'meta_image', 'shortLink', 'previewLink', 'longLink'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'age_range' => 'array',
        'time_range' => 'array',
        'opening_days' => 'array',
        'fees' => 'array',
        'contact' => 'array',
        'social_links' => 'array'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @return BelongsToMany
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(DaycareFeature::class, 'daycare_daycare_feature')->withPivot('active');
    }

  /**
     * @param $value
     * @return string
     */
    public function getDefaultImage($value): string
    {
        if ($value) {
            return $value;
        } else {
            return asset('assets/images/daycare-default.png');
        }
    }

    /**
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(DaycareImage::class);
    }

    /**
     * @return HasOne
     */
    public function adminRatings(): HasOne
    {
        return $this->hasOne(DaycareRating::class)->where('user_id', '=', auth()->id());
    }

    /**
     * @return HasOne
     */
    public function customerRatings(): HasOne
    {
        return $this->hasOne(DaycareRating::class)->where('customer_id', '=', auth('customer')->id());
    }

    /**
     * @return HasMany
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(DaycareRating::class);
    }

    /**
     * A daycare has many categories.
     *
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(DaycareCategory::class, 'daycare_category_daycare');
    }
}

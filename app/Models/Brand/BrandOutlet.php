<?php

namespace App\Models\Brand;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class BrandOutlet extends Model
{
    use Translatable, SoftDeletes, LogsActivity;

    /**
     * Set the translated fields.
     * @var array
     */
    public $translatedAttributes = [
        'name', 'slug', 'short_description'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    // Brand language enum is bn, en
    // Brand type enum is live, recoreded-live, recoreded
    protected $fillable = [
        'status', 'website_link',
        'longitude', 'brand_id', 'latitude',
        'area_id', 'district_id', 'division_id', 'address_line', 'country',
        'google_map_link'
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
    ];

}

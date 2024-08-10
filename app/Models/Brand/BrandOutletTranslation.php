<?php

namespace App\Models\Brand;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;

class BrandOutletTranslation extends Model
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
            'name' => $this->name,
            'short_description' => $this->short_description
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'brand_outlet_id', 'name', 'slug', 
        'short_description'
    ];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['name', 'short_description'];

    /**
     * Set timestamps false.
     * @var bool
     */
    public $timestamps = false;
}

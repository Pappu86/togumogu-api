<?php

namespace App\Models\Daycare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class DaycareTranslation extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'daycare_id', 'name', 'slug', 'description', 'content',
        'meta_title', 'meta_description', 'meta_keyword',
        'hospital_address', 'location', 'division_id', 'district_id', 'area_id',
        'house', 'street', 'zip'
    ];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['name', 'slug'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'location' => 'array',
    ];

    /**
     * Set timestamps false.
     * @var bool
     */
    public $timestamps = false;
}

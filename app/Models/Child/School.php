<?php

namespace App\Models\Child;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Astrotomic\Translatable\Translatable;

/**
 * Class Tag
 * @package App\Models\Common
 */
class School extends Model
{
    use Translatable, LogsActivity, Searchable;
    
    /**
     * Set the translated fields.
     * @var array
     */
    public $translatedAttributes = [
        'name',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'registration_number', 'contact_number', 'website',
        'type', 'area_id', 'address', 'daycare_id' 
    ];

}
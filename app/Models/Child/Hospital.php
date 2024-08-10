<?php

namespace App\Models\Child;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Astrotomic\Translatable\Translatable;

class Hospital extends Model
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
        'address', 'area_id', 'contact_number', 'website', 'status'
    ];
}

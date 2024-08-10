<?php

namespace App\Models\Child;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Child Class
 * @package App\Models\Child
 */
class ChildClass extends Model
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
        'school_id', 'type', 'contact_number', 'website'
    ];

}

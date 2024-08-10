<?php

namespace App\Models\Child;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class SchoolTranslation extends Model
{
    use Searchable;
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'school_id', 'name'
    ];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['name'];

    /**
     * Set timestamps false.
     * @var bool
     */
    public $timestamps = false;
}

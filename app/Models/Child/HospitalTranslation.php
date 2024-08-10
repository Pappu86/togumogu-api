<?php

namespace App\Models\Child;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class HospitalTranslation extends Model
{
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hospital_id', 'name'
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

<?php

namespace App\Models\Child;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChildClassTranslation extends Model
{
    use Searchable, HasFactory;
 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'child_class_id'
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

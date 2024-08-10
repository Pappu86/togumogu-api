<?php

namespace App\Models\Daycare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class DaycareCategoryTranslation extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'daycare_category_id', 'name', 'slug', 'description',
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

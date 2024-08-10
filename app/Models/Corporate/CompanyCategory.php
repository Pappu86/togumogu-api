<?php

namespace App\Models\Corporate;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;

class CompanyCategory extends Model
{
    use HasFactory, Searchable, Translatable, SoftDeletes, LogsActivity;

    /**
     * Set the translated fields.
     *
     * @var array
     */
    public $translatedAttributes = [
        'name', 'slug', 'description',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'image', 'ordering', 'parent_id'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['status', 'parent_id'];
}

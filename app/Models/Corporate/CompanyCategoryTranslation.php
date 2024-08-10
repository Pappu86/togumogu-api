<?php

namespace App\Models\Corporate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CompanyCategoryTranslation extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_category_id', 'name', 'slug', 'description',
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

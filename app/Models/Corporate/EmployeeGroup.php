<?php

namespace App\Models\Corporate;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Corporate\Company;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Astrotomic\Translatable\Translatable;

class EmployeeGroup extends Model
{
    use Translatable, LogsActivity, Searchable;

    /**
     * Set the translated fields.
     * @var array
     */
    public $translatedAttributes = [
        'name', 'slug', 'details'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'company_id'
    ];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['status'];

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

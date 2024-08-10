<?php

namespace App\Models\Corporate;

use Illuminate\Database\Eloquent\Model;
use App\Models\Corporate\CompanyCategory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Astrotomic\Translatable\Translatable;

class Company extends Model
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
        'type', 'status', 'website', 'address', 'logo', 'badge', 'reg_id',
        'details'
    ];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['status'];

    /**
     * A company has many categories.
     *
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(CompanyCategory::class, 'company_category_company');
    }
}

<?php

namespace App\Models\Child;

use Illuminate\Database\Eloquent\Model;
use App\Models\Child\Hospital;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Astrotomic\Translatable\Translatable;

class Doctor extends Model
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
        'registration_number', 'department',
         'website', 'degree', 'hospital_id', 'area_id',
         'contact_number', 'visiting_fee', 'avatar'
    ];

    /**
    * @return BelongsTo
    */
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }
}

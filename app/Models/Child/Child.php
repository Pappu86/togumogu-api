<?php

namespace App\Models\Child;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\Customer;
use App\Models\Child\Doctor;
use App\Models\Child\Hospital;
use App\Models\Child\ChildClass;
use App\Models\Child\School;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyJson;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class Child extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia ;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'children';

   /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 'date_of_birth', 'expecting_date',
        'name', 'avatar', 'gender', 'religion', 'doctor_id', 'school_id', 'child_class_id',
        'blood_group', 'birth_registration_number', 'birth_location',
        'birth_hospital_id', 'parent_status', 'is_default'
    ];

  /**
     * @param $value
     * @return string
     */
    public function getAvatarAttribute($value): string
    {
        if ($value) {
            return $value;
        } else {
            return asset('assets/images/child-default.png');
        }
    }

    /**
     * Register media collection name.
     *
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile();
    }

    /**
    * @return BelongsTo
    */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'parent_id');
    }

    /**
    * @return BelongsTo
    */
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'birth_hospital_id');
    }
    
    /**
    * @return BelongsTo
    */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ChildClass::class, 'child_class_id');
    }

    /**
    * @return BelongsTo
    */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

}

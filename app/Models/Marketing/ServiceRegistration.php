<?php

namespace App\Models\Marketing;

use App\Models\Brand\Brand;
use App\Models\Order\OrderStatus;
use App\Models\User\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class ServiceRegistration extends Model
{
    use SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    // Brand language enum is bn, en
    // Brand type enum is live, recoreded-live, recoreded
    protected $fillable = [
        'status', 'customer_id', 'service_id', 'brand_id',
        'questions', 'customer_info', 'booking_info',
        'service_reg_no', 'comment', 'platform', 'service_reg_status',
        'price', 'special_price', 'payment_method', 'payment_status', 
        'current_latitude', 'current_longitude', 'current_location_name'
    ];

    /**
     * Monitor every fields and add to activity log.
     *
     * @var bool
     */
    protected static $logFillable = true;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'datetime'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * A customer of service
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    
    /**
     * A service
     *
     * @return BelongsTo
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
    
    /**
     * A brand has many categories.
     *
     * @return BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    
    /**
     * @return BelongsTo
     */
    public function serviceRegistrationStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'service_reg_status', 'code');
    }

    /**
     * @return HasMany
     */
    public function processes(): HasMany
    {
        return $this->hasMany(ServiceRegistrationProcess::class)
            ->with(['user', 'orderStatus'])
            ->latest();
    }


}

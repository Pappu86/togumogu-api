<?php

namespace App\Models\Marketing;

use App\Models\Brand\Brand;
use App\Models\User\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class OfferRedeem extends Model
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
        'status', 'spent_reward_point', 'validity_day', 'offer_id',
        'brand_id', 'customer_id', 'start_date',
        'expired_date', 'reward_transaction_id', 'offer_redeem_no'
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
     * A offer redeem has a customer.
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * A offer redeem has a brand.
     *
     * @return BelongsTo
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    /**
     * A offer redeem has a brand.
     *
     * @return BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}

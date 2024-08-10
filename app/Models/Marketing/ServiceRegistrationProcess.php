<?php

namespace App\Models\Marketing;

use App\Models\Order\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRegistrationProcess extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'service_registration_id', 'service_reg_status', 'comment', 'notify'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo( OrderStatus::class, 'service_reg_status', 'code');
    }
}

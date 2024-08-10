<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class PaymentReferance extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id','ref_number','payment_method_code',  'status','customer_id',
    ];

}

<?php

namespace App\Models\Reward;

use App\Models\User\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Reward extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'status', 'redeem', 
        'balance', 'total'
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}

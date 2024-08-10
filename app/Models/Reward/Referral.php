<?php

namespace App\Models\Reward;

use App\Models\Corporate\Partnership;
use App\Models\User\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Referral extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'status', 'type', 
        'partnership_id', 'reference_id', 'uid',
        'dynamic_url', 'preview_url', 'url'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function partnership()
    {
        return $this->belongsTo(Partnership::class);
    }

}

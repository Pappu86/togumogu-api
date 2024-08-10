<?php

namespace App\Models\Reward;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class RewardTransaction extends Model
{
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'customer_id', 'reward_id', 
        'debit', 'credit', 'description', 'reward_setting_id',
        'category', 'reference_id', 'action'
    ];

}

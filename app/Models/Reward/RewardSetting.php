<?php

namespace App\Models\Reward;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class RewardSetting extends Model
{
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'platforms', 'status', 'award_points', 
        'type', 'min_amount', 'max_award_points',
        'start_date', 'end_date', 'category'
    ];

}

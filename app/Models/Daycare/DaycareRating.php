<?php

namespace App\Models\Daycare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaycareRating extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'customer_id', 'daycare_id', 'facility',
        'fee', 'security', 'hygiene', 'care_giving', 'comment',
        'is_approved'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_approved' => 'boolean',
    ];
}

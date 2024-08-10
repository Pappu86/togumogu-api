<?php

namespace App\Models\Daycare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaycareImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'daycare_id', 'src', 'srcset', 'lazy', 'is_featured',
    ];

    /**
     * Set timestamps false.
     *
     * @var bool
     */
    public $timestamps = false;
}

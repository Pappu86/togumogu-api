<?php

namespace App\Models\Community;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id', 'src', 'srcset', 'lazy', 'is_featured',
    ];

    /**
     * Set timestamps false.
     *
     * @var bool
     */
    public $timestamps = false;
}

<?php

namespace App\Models\Community;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Favourite extends Model
{
    use HasFactory, Searchable;

      /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'post_favourites';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'post_id', 
    ];

}

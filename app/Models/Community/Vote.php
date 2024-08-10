<?php

namespace App\Models\Community;

use App\Models\User\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;

class Vote extends Model
{
    use HasFactory, Searchable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'votes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'status', 'post_id', 
        'comment_id', 'like', 'dislike', 'love'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
}

<?php

namespace App\Models\Community;

use App\Models\User\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Comment extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'status', 'content', 
        'commentable_id', 'commentable_type', 'parent_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'commentable_id');
    }
    
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function likes()
    {
        return $this->hasMany(Vote::class)->whereNotNull('like')->where('like', '=', 1);
    }

    public function dislikes()
    {
        return $this->hasMany(Vote::class)->whereNotNull('dislike')->where('dislike', '=', 1);
    }

    public function loves()
    {
        return $this->hasMany(Vote::class)->whereNotNull('love')->where('love', '=', 1);
    }
    
    public function reports()
    {
        return $this->hasMany(Report::class)->latest();
    }

}

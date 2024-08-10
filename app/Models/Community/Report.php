<?php

namespace App\Models\Community;

use App\Models\User\Customer;
use App\Models\NonExistentModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Illuminate\Support\Facades\Log;

class Report extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'status', 'reported_id', 
        'category', 'reason_id', 'note'
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function post()
    {
        if ($this->category === 'post') {
            return $this->belongsTo(Post::class, 'reported_id');
        } else {
            // Return a placeholder relationship with a non-existent model
            return $this->belongsTo(NonExistentModel::class);
        }
    }

    public function comment()
    {
        if ($this->category === 'comment') {
            return $this->belongsTo(Comment::class, 'reported_id');
        } else {
            // Return a placeholder relationship with a non-existent model
            return $this->belongsTo(NonExistentModel::class);
        }
    }

    public function reason()
    {
        return $this->belongsTo(ReportReason::class);
    }

}

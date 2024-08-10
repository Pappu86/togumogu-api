<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Quiz\Question;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizResult extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'quiz_id', 'status',  'name', 'email', 'submission_status',
        'mobile', 'referral_url', 'quiz_score', 'answerer_score',  
        'view_time', 'submit_time', 'taken_time'
    ];

}

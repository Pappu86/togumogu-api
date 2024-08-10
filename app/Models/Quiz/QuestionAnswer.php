<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Quiz\Question;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionAnswer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'status', 'customer_id', 'quiz_id', 'answerer_id', 
       'question_id', 'question_option_id', 'question', 'question_options',
        'is_right_answer', 'answer_option'
    ];

}

<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class QuestionOption extends Model
{
    use Translatable, HasFactory;

    /**
     * Set the translated fields.
     * @var array
     */
    public $translatedAttributes = [
        'text', 'description', 'hint'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'quiz_id', 'question_id', 'image', 'audio', 
        'video', 'is_answer'
    ];

}

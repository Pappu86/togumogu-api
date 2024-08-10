<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class QuestionOptionTranslation extends Model
{
   
    use Searchable;

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'text' => $this->text,
            'hint' => $this->hint
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question_option_id', 'text', 'description', 'hint'
    ];

    /**
     * Set timestamps false.
     *
     * @var bool
     */
    public $timestamps = false;
}

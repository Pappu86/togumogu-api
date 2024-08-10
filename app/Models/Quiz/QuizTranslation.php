<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;

class QuizTranslation extends Model
{
   
    use Searchable, LogsActivity;

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'sub_title' => $this->sub_title,
            'description' => $this->description
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quiz_id', 'title', 'sub_title', 'slug', 'description', 'meta_description',
        'meta_keyword', 'button_text', 'terms_and_conditions', 'ending_msg'
    ];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['title', 'description'];

    /**
     * Set timestamps false.
     *
     * @var bool
     */
    public $timestamps = false;
}

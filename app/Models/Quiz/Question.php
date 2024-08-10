<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Common\Tag;
use App\Models\Quiz\QuestionOption;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Astrotomic\Translatable\Translatable;

class Question extends Model
{
    use Translatable, HasFactory;

    /**
     * Set the translated fields.
     * @var array
     */
    public $translatedAttributes = [
        'title', 'link_text', 'description', 'hint'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'serial_no', 'user_id', 'status',  'quiz_id', 'image', 'audio', 
        'video', 'link', 'time', 'score', 'type', 'is_multiple'
    ];

    /**
     * Each article has many tags.
     *
     * @return MorphToMany
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * A product has many options.
     *
     * @return HasMany
     */
    public function options(): HasMany
    {
        return $this->HasMany(QuestionOption::class, 'question_id');
    }

}

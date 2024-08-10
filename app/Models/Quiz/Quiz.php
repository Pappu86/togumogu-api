<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Quiz\Question;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Astrotomic\Translatable\Translatable;

class Quiz extends Model
{
    use Translatable, LogsActivity, HasFactory;

    /**
     * Set the translated fields.
     * @var array
     */
    public $translatedAttributes = [
        'slug',  'title', 'sub_title', 'description', 'meta_description',
        'meta_keyword', 'button_text', 'terms_and_conditions', 'ending_msg'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'status',  'platforms', 'area', 'max_time', 'total_points', 
        'reward_points', 'image', 'start_date', 'end_date',  
        'meta_keyword', 'button_text', 'dynamic_link', 'is_featured', 
        'color', 'retry_allow', 'tracker', 'tracker_start_day',
        'tracker_end_day', 'tracker_range'
    ];

    /**
     * Monitor every fields and add to activity log.
     *
     * @var bool
     */
    protected static $logFillable = true;

    /**
     * A product has many options.
     *
     * @return HasMany
     */
    public function questions(): HasMany
    {
        return $this->HasMany(Question::class, 'quiz_id')->orderBy('serial_no');
    }

}

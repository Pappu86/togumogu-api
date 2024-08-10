<?php

namespace App\Models\Community;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;

class ReportReasonTranslation extends Model
{
    use Searchable, LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_reason_translations';

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'title' => $this->title
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'report_reason_id', 'title', 'description', 'slug'
    ];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['title'];

    /**
     * Set timestamps false.
     * @var bool
     */
    public $timestamps = false;
}

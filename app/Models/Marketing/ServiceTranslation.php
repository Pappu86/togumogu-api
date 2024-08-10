<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;

class ServiceTranslation extends Model
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
            'short_description' => $this->short_description,
            'long_description' => $this->long_description
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'brand_id', 'title', 'slug', 
        'short_description', 'long_description', 
        'later_btn_text', 'now_btn_text', 'external_btn_text',
        'reg_btn_text', 'booking_btn_text', 'cta_btn_text',
        'special_price_message', 'external_url_btn_text'
    ];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['title', 'short_description'];

    /**
     * Set timestamps false.
     * @var bool
     */
    public $timestamps = false;
}

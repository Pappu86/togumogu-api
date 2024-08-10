<?php

namespace App\Models\Message;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Astrotomic\Translatable\Translatable;

class Template extends Model
{
    use Translatable, LogsActivity, Searchable;

    /**
     * Set the translated fields.
     * @var array
     */
    public $translatedAttributes = [
        'subject', 'content'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_dynamic_value' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'name', 'status', 'main_template_id', 'image', 'category', 'is_dynamic_value',
        'ad_channel_name', 'ad_custom_data', 'ad_sound', 'ad_apple_badge',
        'ad_apple_badge_count', 'ad_expire_value', 'ad_expire_unit',
    ];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['status'];

}

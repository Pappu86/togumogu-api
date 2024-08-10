<?php

namespace App\Models\Message;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;

class CustomNotification extends Model
{
    use LogsActivity, Searchable;

    // /**
    //  * Set the translated fields.
    //  * @var array
    //  */
    // public $translatedAttributes = [
    //     'name',
    // ];

    /***
     * Here custome notification types are 3
     * sms, push_notification, email, database
     * 
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'status', 'name', 'platform', 'template_id',
        'process_status', 'scheduling_type', 'scheduling_date',
        'target', 'is_android', 'is_ios', 'notification_type',
        'period', 'activity', 'days', 
    ];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['status'];

}

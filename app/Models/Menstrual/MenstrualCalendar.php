<?php

namespace App\Models\Menstrual;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class MenstrualCalendar extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'status', 'month', 'start_date', 'active_days', 'cycle_length', 'ovulation_date', 'next_cycle_date',
        'next_ovulation_date',
    ];

    /**
     * Monitor every fields and add to activity log.
     *
     * @var bool
     */
    protected static $logFillable = true;
}

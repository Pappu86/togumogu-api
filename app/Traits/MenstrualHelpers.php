<?php

namespace App\Traits;

use App\Models\Menstrual\MenstrualCalendar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait MenstrualHelpers {

    public function menstrualDateCalculation($start_date, $activeDays, $cycleLength)
    {
        if(!isset($activeDays)){
            $activeDays = 4;
        }
        
        if(!isset($cycleLength)){
            $cycleLength = 28;
        }

        $now=Carbon::now();
        $startDate = $now->parse($start_date);
        $menstrualMonth=$now->parse($start_date)->format('F Y');
        $predictedNextCycleDate = $now->parse($start_date)->addDays(($cycleLength));
        $predictedOvalationDate = $now->parse($start_date)->addDays((13));
        $nextPredictedOvalationDate = $now->parse($predictedNextCycleDate)->addDays((13));

        return  [
            'start_date' => $startDate,
            'month' => $menstrualMonth,
            'active_days' => $activeDays ,
            'cycle_length' => $cycleLength,
            'next_cycle_date'=>$predictedNextCycleDate,
            'ovulation_date'=>$predictedOvalationDate,
            'next_ovulation_date'=>$nextPredictedOvalationDate
        ];
    }
}